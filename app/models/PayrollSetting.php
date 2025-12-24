<?php
// app/models/PayrollSetting.php

class PayrollSetting {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function getAllSettings() {
        $this->db->query("SELECT * FROM payroll_settings ORDER BY setting_year DESC");
        return $this->db->fetchAll();
    }

    public function createOrUpdate($data) {
        $this->db->query("SELECT id FROM payroll_settings WHERE setting_year = :year");
        $this->db->bind(':year', $data['setting_year']);
        $existing = $this->db->fetch();

         if ($existing) {
            $sql = "UPDATE payroll_settings SET base_salary_monthly = :base_salary_monthly, work_days_in_month = :work_days_in_month, housing_allowance = :housing_allowance, family_allowance = :family_allowance, seniority_per_year = :seniority_per_year, bonus_rate_percentage = :bonus_rate_percentage, penalty_rate_percentage = :penalty_rate_percentage WHERE setting_year = :setting_year";
        } else {
            $sql = "INSERT INTO payroll_settings (setting_year, base_salary_monthly, work_days_in_month, housing_allowance, family_allowance, seniority_per_year, bonus_rate_percentage, penalty_rate_percentage) VALUES (:setting_year, :base_salary_monthly, :work_days_in_month, :housing_allowance, :family_allowance, :seniority_per_year, :bonus_rate_percentage, :penalty_rate_percentage)";
        }
        
        $this->db->query($sql);
        
        $this->db->bind(':setting_year', $data['setting_year']);
        $this->db->bind(':base_salary_monthly', $data['base_salary_monthly']);
        $this->db->bind(':work_days_in_month', $data['work_days_in_month']);
        $this->db->bind(':housing_allowance', $data['housing_allowance']);
        $this->db->bind(':family_allowance', $data['family_allowance']);
        $this->db->bind(':seniority_per_year', $data['seniority_per_year']);
        $this->db->bind(':bonus_rate_percentage', $data['bonus_rate_percentage']);
        $this->db->bind(':penalty_rate_percentage', $data['penalty_rate_percentage']);
        
        return $this->db->execute();
    }
    public function getSettingsByYear($year) {
    $this->db->query('SELECT * FROM payroll_settings WHERE setting_year = :year LIMIT 1');
    $this->db->bind(':year', $year);
    return $this->db->fetch();
}
}