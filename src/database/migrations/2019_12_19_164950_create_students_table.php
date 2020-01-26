<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('students', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->softDeletes();
            $table->rememberToken();
            $table->boolean('old_version')->default(0)->comment('版本備份');
            $table->bigInteger('origin_id')->default(0)->comment('版本原始id');
            $table->bigInteger('update_user_id')->default(0)->comment('記錄更新人員');
            $table->bigInteger('sort')->default(0)->comment('排序');
            $table->enum('status', ['啟用', '停用'])->default('啟用')->comment('狀態');
            $table->text('note')->nullable()->charset('utf8')->comment('備註');

            $table->string('student_name')->charset('utf8')->comment('學生姓名');
            $table->string('student_class')->charset('utf8')->comment('班級名稱');
            $table->string('seat_number')->charset('utf8')->comment('座號');
            $table->string('school_student_id')->charset('utf8')->comment('學號-帳號');
            $table->string('password')->comment('密碼');
            $table->string('email')->nullable()->charset('utf8')->comment('電子郵件');
            $table->string('educational_system')->nullable()->charset('utf8')->comment('學制');
            $table->year('graduation_year')->nullable()->comment('畢業年度');
            $table->string('department')->nullable()->charset('utf8')->comment('科系');
            $table->string('school_year')->nullable()->charset('utf8')->comment('入學年度');
            $table->enum('gender', ['男', '女'])->nullable()->comment('性別');
            $table->string('id_number')->nullable()->charset('utf8')->comment('身份證字號');
            $table->date('birthday')->nullable()->comment('出生年月日');
            $table->string('tel')->nullable()->charset('utf8')->comment('電話');
            $table->string('city')->nullable()->charset('utf8')->comment('縣市');
            $table->string('area')->nullable()->charset('utf8')->comment('地區');
            $table->string('address')->nullable()->charset('utf8')->comment('地址');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('students');
    }
}
