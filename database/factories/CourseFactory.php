<?php

namespace Database\Factories;

use App\Constants\CourseConstants;
use App\Enums\CourseStatus;
use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;

class CourseFactory extends Factory
{

    public function definition(): array
    {
        return [
            CourseConstants::FIELD_COURSE_TITLE => $this->faker->sentence(3),
            CourseConstants::FIELD_COURSE_DESCRIPTION => $this->faker->paragraph(),
            CourseConstants::FIELD_STATUS => $this->faker->randomElement(CourseStatus::cases())->value,
            CourseConstants::FIELD_IS_PREMIUM => $this->faker->boolean(),
        ];
    }


    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            CourseConstants::FIELD_STATUS => CourseStatus::PUBLISHED->value,
        ]);
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            CourseConstants::FIELD_STATUS => CourseStatus::PENDING->value,
        ]);
    }

    public function premium(): static
    {
        return $this->state(fn (array $attributes) => [
            CourseConstants::FIELD_IS_PREMIUM => true,
        ]);
    }

    public function free(): static
    {
        return $this->state(fn (array $attributes) => [
            CourseConstants::FIELD_IS_PREMIUM => false,
        ]);
    }
}
