<?php

use App\Constants\CourseConstants;
use App\Enums\CourseStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(CourseConstants::TABLE_COURSES, function (Blueprint $table) {
            $table->id();
            $table->string(CourseConstants::FIELD_COURSE_TITLE)->required();
            $table->text(CourseConstants::FIELD_COURSE_DESCRIPTION)->nullable();
            $table->enum(CourseConstants::FIELD_STATUS, CourseStatus::values())->default(CourseStatus::PENDING->value);
            $table->boolean(CourseConstants::FIELD_IS_PREMIUM)->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(CourseConstants::TABLE_COURSES);
    }
};
