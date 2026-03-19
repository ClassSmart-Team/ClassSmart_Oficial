<?php
 
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\AssignmentController;
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
        Route::apiResource('users', UserController::class);
        Route::apiResource('roles', RoleController::class);
        Route::apiResource('periods', PeriodController::class);
        Route::apiResource('schedules', ScheduleController::class);
    });
 
 
    // Admin y Maestro (roles 1 y 2)
    Route::middleware('role:1,2')->group(function () {
        Route::apiResource('groups', GroupController::class);
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
    });
 

    // Solo Alumno (role 3)
    Route::middleware('role:3')->group(function () {
        Route::post('submissions', [SubmissionController::class, 'store']);
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
 
        // Chats y mensajes — padres excluidos
        Route::apiResource('chats', ChatController::class);
        Route::apiResource('messages', MessageController::class);
    });
 
});