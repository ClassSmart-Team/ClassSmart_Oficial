<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\AuditController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\ConfigurationController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\GradeRecordController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\GroupFileController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PeriodController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\SubmissionController;
use App\Http\Controllers\UnitController;

/*Roles de Cajon
1 = Admin
2 = Teacher
3 = Student
4 = Parent
*/

// Rutas públicas (sin token)
Route::get('/test-mail', [AuthController::class, 'testMail']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// Rutas protegidas (piden token))
Route::middleware('auth:sanctum')->group(function () {

    // Auth (cualquier usuario autentificado)
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // Configuración personal (todos los roles)
    Route::get('configurations', [ConfigurationController::class, 'show']);
    Route::patch('configurations', [ConfigurationController::class, 'update']);

    // Notificaciones - ver y marcar como leída (todos los roles)
    Route::get('notifications', [NotificationController::class, 'index']);
    Route::get('notifications/{id}', [NotificationController::class, 'show']);
    Route::patch('notifications/{notification}/read', [NotificationController::class, 'markAsRead']);

    // Calificaciones — ver: todos los roles (padre ve las de sus hijos)
    Route::get('grade-records', [GradeRecordController::class, 'index']);
    Route::get('grade-records/{id}', [GradeRecordController::class, 'show']);

    // Admin (Rol 1)
    Route::middleware('role:1')->group(function () {
        Route::get('audits', [AuditController::class, 'index']);
        Route::get("users", [UserController::class, 'index']);
        Route::post("users", [UserController::class, 'store']);
        Route::get("users/{id}", [UserController::class, 'show']);
        Route::put("users/{id}", [UserController::class, 'update']);
        Route::delete("users/{id}", [UserController::class, 'destroy']);
        Route::apiResource('roles', RoleController::class);
        Route::post("periods", [PeriodController::class, 'store']);
        Route::get("periods/{id}", [PeriodController::class, 'show']);
        Route::put("periods/{id}", [PeriodController::class, 'update']);
        Route::delete("periods/{id}", [PeriodController::class, 'destroy']);
        Route::get("schedules", [ScheduleController::class, 'index']);
        Route::post("schedules", [ScheduleController::class, 'store']);
        Route::get("schedules/{id}", [ScheduleController::class, 'show']);
        Route::put("schedules/{id}", [ScheduleController::class, 'update']);
        Route::delete("schedules/{id}", [ScheduleController::class, 'destroy']);
    });


    /* Admin, Maestro y Alumno (roles 1, 2 y 3) */
    Route::middleware('role:1,2,3')->group(function () {

        // Ver y eliminar entregas
        Route::get('submissions', [SubmissionController::class, 'index']);
        Route::get('submissions/{id}', [SubmissionController::class, 'show']);
        Route::delete('submissions/{id}', [SubmissionController::class, 'destroy']);

        // Archivos
        Route::apiResource('files', FileController::class);
        Route::apiResource('group-files', GroupFileController::class);

        Route::get("/files/{id}/view", function ($id) {
            $file = \App\Models\File::findOrFail($id);
            return response()->file(storage_path('app/public' . $file->file_path));
        });")

        // Chats y mensajes - padres excluidos
        Route::apiResource('chats', ChatController::class);
        Route::apiResource('messages', MessageController::class);
    });

    // Admin y Maestro (roles 1 y 2)
    Route::middleware('role:1,2')->group(function () {
        // Grupos - consulta
        Route::get('groups/available-students/{id}', [GroupController::class, 'getAvailableStudents']);
        Route::get('groups', [GroupController::class, 'index']);
        Route::get('groups/{id}', [GroupController::class, 'show']);
        Route::post('groups', [GroupController::class, 'store']);
        Route::put('groups/{id}', [GroupController::class, 'update']);
        Route::delete('groups/{id}', [GroupController::class, 'destroy']);
        Route::post('groups/{group}/students', [GroupController::class, 'addStudent']);
        Route::delete('groups/{group}/students', [GroupController::class, 'removeStudent']);

        Route::apiResource('units', UnitController::class);
        Route::apiResource('announcements', AnnouncementController::class);
        Route::apiResource('assignments', AssignmentController::class);

        // Calificaciones — crear, editar, eliminar
        Route::post('grade-records', [GradeRecordController::class, 'store']);
        Route::put('grade-records/{id}', [GradeRecordController::class, 'update']);
        Route::delete('grade-records/{id}', [GradeRecordController::class, 'destroy']);

        // Notificaciones — crear y eliminar
        Route::post('notifications', [NotificationController::class, 'store']);
        Route::delete('notifications/{id}', [NotificationController::class, 'destroy']);

        // Calificar entregas
        Route::patch('submissions/{submission}/grade', [SubmissionController::class, 'grade']);

        //Horarios
        Route::get("periods", [PeriodController::class, 'index']);
    });


    // Solo Alumno (role 3)
    Route::middleware('role:3')->group(function () {
        Route::get('my-groups', [GroupController::class, 'myGroups']);
        Route::get('my-groups/{id}', [GroupController::class, 'myGroupShow']);
        Route::get('my-assignments', [AssignmentController::class, 'myAssignments']);
        Route::get('my-assignments/{id}', [AssignmentController::class, 'myAssignmentShow']);
        Route::get('my-submissions', [SubmissionController::class, 'mySubmissions']);
        Route::get('my-submissions/{id}', [SubmissionController::class, 'mySubmissionShow']);
        Route::get('my-assignment-submissions/{assignment}', [SubmissionController::class, 'myAssignmentSubmissions']);
        Route::post('submissions', [SubmissionController::class, 'store']);
    });

});
