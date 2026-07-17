<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            if (!$this->hasIndex('attendances', 'attendances_user_id_index')) {
                $table->index('user_id');
            }
            if (!$this->hasIndex('attendances', 'attendances_date_index')) {
                $table->index('date');
            }
            if (!$this->hasIndex('attendances', 'attendances_user_id_date_index')) {
                $table->index(['user_id', 'date']);
            }
            if (!$this->hasIndex('attendances', 'attendances_approved_by_index')) {
                $table->index('approved_by');
            }
        });

        Schema::table('attendance_exceptions', function (Blueprint $table) {
            if (!$this->hasIndex('attendance_exceptions', 'attendance_exceptions_user_id_index')) {
                $table->index('user_id');
            }
            if (!$this->hasIndex('attendance_exceptions', 'attendance_exceptions_status_index')) {
                $table->index('status');
            }
        });

        Schema::table('students', function (Blueprint $table) {
            if (!$this->hasIndex('students', 'students_supervisor_id_index')) {
                $table->index('supervisor_id');
            }
            if (!$this->hasIndex('students', 'students_user_id_index')) {
                $table->index('user_id');
            }
        });

        Schema::table('supervisors', function (Blueprint $table) {
            if (!$this->hasIndex('supervisors', 'supervisors_user_id_index')) {
                $table->index('user_id');
            }
        });

        Schema::table('tasks', function (Blueprint $table) {
            if (!$this->hasIndex('tasks', 'tasks_supervisor_id_index')) {
                $table->index('supervisor_id');
            }
            if (!$this->hasIndex('tasks', 'tasks_created_at_index')) {
                $table->index('created_at');
            }
        });

        Schema::table('submissions', function (Blueprint $table) {
            if (!$this->hasIndex('submissions', 'submissions_task_id_index')) {
                $table->index('task_id');
            }
            if (!$this->hasIndex('submissions', 'submissions_student_id_index')) {
                $table->index('student_id');
            }
            if (!$this->hasIndex('submissions', 'submissions_task_id_student_id_index')) {
                $table->index(['task_id', 'student_id']);
            }
        });

        Schema::table('documents', function (Blueprint $table) {
            if (!$this->hasIndex('documents', 'documents_student_id_index')) {
                $table->index('student_id');
            }
            if (!$this->hasIndex('documents', 'documents_type_index')) {
                $table->index('type');
            }
        });

        if (Schema::hasTable('logbooks')) {
            Schema::table('logbooks', function (Blueprint $table) {
                if (!$this->hasIndex('logbooks', 'logbooks_student_id_index')) {
                    $table->index('student_id');
                }
                if (!$this->hasIndex('logbooks', 'logbooks_activity_date_index')) {
                    $table->index('activity_date');
                }
            });
        }

        if (Schema::hasTable('final_assessments')) {
            Schema::table('final_assessments', function (Blueprint $table) {
                if (!$this->hasIndex('final_assessments', 'final_assessments_student_id_index')) {
                    $table->index('student_id');
                }
            });
        }

        if (Schema::hasTable('sessions')) {
            Schema::table('sessions', function (Blueprint $table) {
                if (!$this->hasIndex('sessions', 'sessions_last_activity_index')) {
                    $table->index('last_activity');
                }
            });
        }
    }

    private function hasIndex($table, $indexName)
    {
        return \DB::select("SHOW INDEX FROM {$table} WHERE Key_name = ?", [$indexName]) ? true : false;
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropIndexIfExists(['user_id']);
            $table->dropIndexIfExists(['date']);
            $table->dropIndexIfExists(['user_id', 'date']);
            $table->dropIndexIfExists(['approved_by']);
        });

        Schema::table('attendance_exceptions', function (Blueprint $table) {
            $table->dropIndexIfExists(['user_id']);
            $table->dropIndexIfExists(['status']);
        });

        Schema::table('students', function (Blueprint $table) {
            $table->dropIndexIfExists(['supervisor_id']);
            $table->dropIndexIfExists(['user_id']);
        });

        Schema::table('supervisors', function (Blueprint $table) {
            $table->dropIndexIfExists(['user_id']);
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->dropIndexIfExists(['supervisor_id']);
            $table->dropIndexIfExists(['created_at']);
        });

        Schema::table('submissions', function (Blueprint $table) {
            $table->dropIndexIfExists(['task_id']);
            $table->dropIndexIfExists(['student_id']);
            $table->dropIndexIfExists(['task_id', 'student_id']);
        });

        Schema::table('documents', function (Blueprint $table) {
            $table->dropIndexIfExists(['student_id']);
            $table->dropIndexIfExists(['type']);
        });

        if (Schema::hasTable('logbooks')) {
            Schema::table('logbooks', function (Blueprint $table) {
                $table->dropIndexIfExists(['student_id']);
                $table->dropIndexIfExists(['activity_date']);
            });
        }

        if (Schema::hasTable('final_assessments')) {
            Schema::table('final_assessments', function (Blueprint $table) {
                $table->dropIndexIfExists(['student_id']);
            });
        }

        if (Schema::hasTable('sessions')) {
            Schema::table('sessions', function (Blueprint $table) {
                $table->dropIndexIfExists(['last_activity']);
            });
        }
    }
};
