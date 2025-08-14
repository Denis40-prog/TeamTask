<?php

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

// 1) Tous les tests (Feature & Unit) utilisent la TestCase Laravel
uses(TestCase::class)->in('Feature', 'Unit');

// 2) Tous les tests (Feature & Unit) ont une base fraîche (migrations)
uses(RefreshDatabase::class)->in('Feature', 'Unit');

// (facultatif) helpers d'expectations
expect()->extend('toBeOne', fn () => $this->toBe(1));
