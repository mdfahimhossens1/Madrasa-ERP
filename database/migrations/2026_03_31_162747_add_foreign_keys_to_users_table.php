<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // প্রথমে sections টেবিলের foreign keys (এটা আগে করতে হবে)
        Schema::table('sections', function (Blueprint $table) {
            $table->foreign('class_id')->references('id')->on('classes')->onDelete('cascade');
            $table->foreign('institution_id')->references('id')->on('madrasas')->onDelete('cascade');
        });
        
        // তারপর students টেবিলের foreign keys
        Schema::table('students', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('institution_id')->references('id')->on('madrasas')->onDelete('cascade');
            $table->foreign('academic_year_id')->references('id')->on('academic_years');
            $table->foreign('section_id')->references('id')->on('sections')->onDelete('set null');
            $table->foreign('guardian_user_id')->references('id')->on('users')->onDelete('set null');
        });
        
        // সবশেষে users টেবিলের foreign keys
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('restrict');
            $table->foreign('institution_id')->references('id')->on('madrasas')->onDelete('cascade');
            $table->foreign('present_division_id')->references('id')->on('divisions')->onDelete('set null');
            $table->foreign('present_district_id')->references('id')->on('districts')->onDelete('set null');
            $table->foreign('present_upazila_id')->references('id')->on('upazilas')->onDelete('set null');
            $table->foreign('permanent_division_id')->references('id')->on('divisions')->onDelete('set null');
            $table->foreign('permanent_district_id')->references('id')->on('districts')->onDelete('set null');
            $table->foreign('permanent_upazila_id')->references('id')->on('upazilas')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
        });

    }

    public function down(): void
    {
        // Reverse order: প্রথমে users, তারপর students, তারপর sections
        
        // Users টেবিলের foreign keys ড্রপ
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropForeign(['institution_id']);
            $table->dropForeign(['present_division_id']);
            $table->dropForeign(['present_district_id']);
            $table->dropForeign(['present_upazila_id']);
            $table->dropForeign(['permanent_division_id']);
            $table->dropForeign(['permanent_district_id']);
            $table->dropForeign(['permanent_upazila_id']);
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);
        });
        
        // Students টেবিলের foreign keys ড্রপ
        Schema::table('students', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['institution_id']);
            $table->dropForeign(['academic_year_id']);
            $table->dropForeign(['section_id']);
            $table->dropForeign(['guardian_user_id']);
        });
        
        // Sections টেবিলের foreign keys ড্রপ
        Schema::table('sections', function (Blueprint $table) {
            $table->dropForeign(['class_id']);
            $table->dropForeign(['institution_id']);
        });

    }
};