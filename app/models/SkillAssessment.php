<?php
// app/models/SkillAssessment.php

class SkillAssessment {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    /**
     * ذخیره نمرات ارزیابی
     * @param array $data شامل user_id, evaluator_id, evaluation_type, year, scores, notes
     * @return bool
     */
    public function saveScores($data) {
        $this->db->beginTransaction();
        try {
            foreach ($data['scores'] as $skill_category => $score) {
                if (!empty($score)) {
                    $this->db->query('INSERT INTO skill_assessment_scores (user_id, evaluator_id, evaluation_type, year, skill_category, score, notes) VALUES (:user_id, :evaluator_id, :evaluation_type, :year, :skill_category, :score, :notes)');
                    $this->db->bind(':user_id', $data['user_id']);
                    $this->db->bind(':evaluator_id', $data['evaluator_id']);
                    $this->db->bind(':evaluation_type', $data['evaluation_type']);
                    $this->db->bind(':year', $data['year']);
                    $this->db->bind(':skill_category', $skill_category);
                    $this->db->bind(':score', $score);
                    $this->db->bind(':notes', $data['notes']);
                    $this->db->execute();
                }
            }
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Error saving assessment scores: " . $e->getMessage());
            return false;
        }
    }

    /**
     * بررسی وجود ارزیابی قبلی برای یک نوع خاص (مثلا خودارزیابی)
     */
    public function hasAssessmentBeenSubmitted($userId, $year, $type) {
        $this->db->query('SELECT COUNT(*) as count FROM skill_assessment_scores WHERE user_id = :user_id AND year = :year AND evaluation_type = :type LIMIT 1');
        $this->db->bind(':user_id', $userId);
        $this->db->bind(':year', $year);
        $this->db->bind(':type', $type);
        $result = $this->db->fetch();
        return $result && $result->count > 0;
    }

    /**
     * دریافت ارزیابی‌های انجام شده توسط یک کاربر برای دیگران
     */
    public function getAssessmentsByEvaluator($evaluatorId, $year) {
        $this->db->query('SELECT DISTINCT user_id FROM skill_assessment_scores WHERE evaluator_id = :evaluator_id AND year = :year AND evaluation_type = "peer"');
        $this->db->bind(':evaluator_id', $evaluatorId);
        $this->db->bind(':year', $year);
        $results = $this->db->fetchAll();
        return array_column($results, 'user_id');
    }
}
