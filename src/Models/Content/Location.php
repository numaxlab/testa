<?php

namespace Trafikrak\Models\Content;

enum Location: string
{
    case USER_DASHBOARD_SUBSCRIPTIONS = 'user-dashboard-subscriptions';
    case COURSE = 'course';
    case COURSE_REGISTER = 'course-register';
}
