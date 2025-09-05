<?php
// app/models/TrainingNeed.php

class TrainingNeed {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function create($data) {
        $this->db->query('INSERT INTO training_needs (user_id, year, strengths, weaknesses) VALUES (:user_id, :year, :strengths, :weaknesses)');
        $this->db->bind(':user_id', $data['user_id']);
        $this->db->bind(':year', $data['year']);
        $this->db->bind(':strengths', $data['strengths']);
        $this->db->bind(':weaknesses', $data['weaknesses']);
        return $this->db->execute();
    }
    
    public function getNeedByUserIdAndYear($userId, $year) {
        $this->db->query('SELECT * FROM training_needs WHERE user_id = :user_id AND year = :year LIMIT 1');
        $this->db->bind(':user_id', $userId);
        $this->db->bind(':year', $year);
        return $this->db->fetch();
    }

    public function getAllPending() {
        $this->db->query('SELECT tn.*, u.name as employee_name FROM training_needs tn JOIN users u ON tn.user_id = u.id WHERE tn.status = "pending" ORDER BY tn.created_at DESC');
        return $this->db->fetchAll();
    }
    
    public function findById($id) {
        $this->db->query('SELECT * FROM training_needs WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->fetch();
    }

    public function update($id, $data) {
        $this->db->query('UPDATE training_needs SET development_areas = :development_areas, course_suggestions = :course_suggestions, status = :status, manager_id = :manager_id WHERE id = :id');
        $this->db->bind(':development_areas', $data['development_areas']);
        $this->db->bind(':course_suggestions', $data['course_suggestions']);
        $this->db->bind(':status', $data['status']);
        $this->db->bind(':manager_id', $data['manager_id']);
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    /**
     * متد جدید: نتایج آزمون ۳۶۰ درجه یک کارمند را برمی‌گرداند.
     * @param int $userId
     * @param int $year
     * @return array
     */
    public function getAssessmentResults($userId, $year) {
        $this->db->query("
            SELECT
                sar.evaluation_type,
                sar.skill_category,
                AVG(sar.score) as avg_score
            FROM skill_assessment_scores sar
            WHERE sar.user_id = :user_id AND sar.year = :year
            GROUP BY sar.evaluation_type, sar.skill_category
        ");
        $this->db->bind(':user_id', $userId);
        $this->db->bind(':year', $year);
        return $this->db->fetchAll();
    }
    
    /**
     * متد جدید: نتایج تحلیلی گروهی سازمان را برمی‌گرداند.
     * @param int $year
     * @param string $role
     * @return array
     */
    public function getGroupAssessmentAnalysis($year, $role = 'employee') {
        $this->db->query("
            SELECT
                sar.skill_category,
                AVG(CASE WHEN sar.evaluation_type = 'self' THEN sar.score END) as self_score,
                AVG(CASE WHEN sar.evaluation_type = 'manager' THEN sar.score END) as manager_score,
                AVG(CASE WHEN sar.evaluation_type = 'peer' THEN sar.score END) as peer_score,
                AVG(sar.score) as overall_score
            FROM skill_assessment_scores sar
            JOIN users u ON sar.user_id = u.id
            WHERE sar.year = :year AND u.role = :role
            GROUP BY sar.skill_category
            ORDER BY overall_score
        ");
        $this->db->bind(':year', $year);
        $this->db->bind(':role', $role);
        return $this->db->fetchAll();
    }
}
