<?php


namespace Modules\Admin\Enums;

enum Role: string
{
    case SUPER_ADMIN = 'Super Admin';
    case TEACHER = 'Teacher';
    case STUDENT = 'Student';

}
