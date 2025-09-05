<?php
// app/models/Project.php

class Project {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function getAllProjects() {
        $this->db->query("
            SELECT projects.*, clients.name as client_name 
            FROM projects 
            LEFT JOIN clients ON projects.client_id = clients.id 
            ORDER BY projects.created_at DESC
        ");
        return $this->db->fetchAll();
    }

    public function findById($id) {
        $this->db->query('SELECT * FROM projects WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->fetch();
    }

    public function create($data) {
        $this->db->query('INSERT INTO projects (client_id, contract_id, category_id, name, description, start_date, due_date, budget, status) VALUES (:client_id, :contract_id, :category_id, :name, :description, :start_date, :due_date, :budget, :status)');
    
        $this->db->bind(':client_id', $data['client_id']);
        $this->db->bind(':contract_id', $data['contract_id'] ?? null);
        $this->db->bind(':category_id', $data['category_id'] ?? null);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':start_date', $data['start_date']);
        $this->db->bind(':due_date', $data['due_date']);
        $this->db->bind(':budget', $data['budget']);
        $this->db->bind(':status', $data['status']);

        if ($this->db->execute()) {
            return $this->db->lastInsertId();
        }
        return false;
    }

    public function update($id, $data) {
        $this->db->query('UPDATE projects SET client_id = :client_id, contract_id = :contract_id, category_id = :category_id, name = :name, description = :description, start_date = :start_date, due_date = :due_date, budget = :budget, status = :status WHERE id = :id');
        
        $this->db->bind(':id', $id);
        $this->db->bind(':client_id', $data['client_id']);
        $this->db->bind(':contract_id', $data['contract_id']);
        $this->db->bind(':category_id', $data['category_id'] ?? null);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':start_date', $data['start_date']);
        $this->db->bind(':due_date', $data['due_date']);
        $this->db->bind(':budget', $data['budget']);
        $this->db->bind(':status', $data['status']);

        return $this->db->execute();
    }

    public function delete($id) {
        $this->db->query('DELETE FROM projects WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
    
    /**
     * Retrieves all projects with details and applies filters.
     * @param array $filters
     * @return array
     */
    public function getFilteredProjects($filters = []) {
        $sql = "
            SELECT p.*, c.name as client_name, cat.name as category_name,
                   (SELECT COUNT(*) FROM tasks WHERE project_id = p.id) as total_tasks,
                   (SELECT COUNT(*) FROM tasks WHERE project_id = p.id AND status = 'done') as completed_tasks
            FROM projects p
            LEFT JOIN clients c ON p.client_id = c.id
            LEFT JOIN categories cat ON p.category_id = cat.id
            WHERE 1=1
        ";

        if (!empty($filters['search'])) {
            $sql .= " AND p.name LIKE :search";
        }
        if (!empty($filters['category_id'])) {
            $sql .= " AND p.category_id = :category_id";
        }
        if (!empty($filters['status'])) {
            $sql .= " AND p.status = :status";
        }
        $sql .= " ORDER BY p.created_at DESC";

        $this->db->query($sql);

        if (!empty($filters['search'])) {
            $this->db->bind(':search', '%' . $filters['search'] . '%');
        }
        if (!empty($filters['category_id'])) {
            $this->db->bind(':category_id', $filters['category_id']);
        }
        if (!empty($filters['status'])) {
            $this->db->bind(':status', $filters['status']);
        }
        
        return $this->db->fetchAll();
    }

    public function getProjectStatusSummary() {
        $this->db->query("
            SELECT status, COUNT(*) AS count
            FROM projects
            GROUP BY status
        ");
        $results = $this->db->fetchAll();
        
        $summary = [
            'not_started' => 0,
            'in_progress' => 0,
            'finished' => 0,
            'on_hold' => 0,
            'canceled' => 0,
            'total' => 0,
        ];
        
        foreach ($results as $row) {
            if (isset($summary[$row->status])) {
                $summary[$row->status] = $row->count;
            }
            $summary['total'] += $row->count;
        }
        
        return $summary;
    }

    public function getProjectMembers($projectId) {
        $this->db->query('SELECT u.id, u.name, u.email, pm.role FROM project_members pm JOIN users u ON pm.user_id = u.id WHERE pm.project_id = :project_id');
        $this->db->bind(':project_id', $projectId);
        return $this->db->fetchAll();
    }
    
    public function getProjectsByUserId($userId) {
        $this->db->query('
            SELECT p.*, cat.name as category_name FROM projects p
            JOIN project_members pm ON p.id = pm.project_id
            LEFT JOIN categories cat ON p.category_id = cat.id
            WHERE pm.user_id = :user_id
            ORDER BY p.created_at DESC
        ');
        $this->db->bind(':user_id', $userId);
        return $this->db->fetchAll();
    }

    public function getProjectsByClientId($clientId) {
        $this->db->query("
            SELECT p.*, c.name as client_name, cat.name as category_name,
                   (SELECT COUNT(*) FROM tasks WHERE project_id = p.id) as total_tasks,
                   (SELECT COUNT(*) FROM tasks WHERE project_id = p.id AND status = 'done') as completed_tasks
            FROM projects p
            LEFT JOIN clients c ON p.client_id = c.id
            LEFT JOIN categories cat ON p.category_id = cat.id
            WHERE p.client_id = :client_id
            ORDER BY p.created_at DESC
        ");
        $this->db->bind(':client_id', $clientId);
        return $this->db->fetchAll();
    }
    public function addMember($projectId, $userId, $role) {
    $this->db->query('INSERT INTO project_members (project_id, user_id, role) VALUES (:project_id, :user_id, :role)');
    $this->db->bind(':project_id', $projectId);
    $this->db->bind(':user_id', $userId);
    $this->db->bind(':role', $role);
    return $this->db->execute();
}
}