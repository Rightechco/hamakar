<?php
// app/controllers/MarketController.php

class MarketController {
    
    public function index() {
        // چون سیستم شما بر پایه سوییچ در index.php است، از تابع view داخلی پروژه استفاده می‌کنیم
        view('market/index', [
            'layout' => 'guest_layout', 
            'title' => 'فروشگاه ماژول‌ها'
        ]);
    }

    public function windows() {
        view('market/windows', [
            'layout' => 'guest_layout', 
            'title' => 'نرم‌افزار ویندوز'
        ]);
    }
}