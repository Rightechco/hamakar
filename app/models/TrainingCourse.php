<?php
// app/models/TrainingCourse.php

class TrainingCourse {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    /**
     * تمام دوره‌های آموزشی را برمی‌گرداند.
     * @return array
     */
    public function getAll() {
        $this->db->query('SELECT * FROM training_courses ORDER BY created_at DESC');
        return $this->db->fetchAll();
    }

    /**
     * یک دوره آموزشی جدید ایجاد می‌کند.
     * @param array $data
     * @return bool
     */
    public function create($data) {
        $this->db->query('INSERT INTO training_courses (course_title, description, target_audience) VALUES (:course_title, :description, :target_audience)');
        $this->db->bind(':course_title', $data['course_title']);
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':target_audience', $data['target_audience']);
        return $this->db->execute();
    }

    /**
     * گزارشی تحلیلی از نیازهای آموزشی جمع‌آوری شده برمی‌گرداند.
     * @param int $year
     * @return array
     */
    public function getAnalysisReport($year) {
        // تحلیل نقاط ضعف تکراری
        $this->db->query('SELECT weaknesses, COUNT(*) as count FROM training_needs WHERE year = :year AND weaknesses IS NOT NULL GROUP BY weaknesses ORDER BY count DESC');
        $this->db->bind(':year', $year);
        $weaknessAnalysis = $this->db->fetchAll();

        // تحلیل دوره‌های پیشنهادی تکراری
        $this->db->query('SELECT course_suggestions, COUNT(*) as count FROM training_needs WHERE year = :year AND course_suggestions IS NOT NULL GROUP BY course_suggestions ORDER BY count DESC');
        $this->db->bind(':year', $year);
        $courseAnalysis = $this->db->fetchAll();

        return [
            'weakness_analysis' => $weaknessAnalysis,
            'course_analysis' => $courseAnalysis
        ];
    }
}
