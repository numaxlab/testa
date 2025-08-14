<?php

namespace Trafikrak\Models\Education;

enum CourseDeliveryMethod: string
{
    case IN_PERSON = 'in_person';
    case ONLINE = 'online';
    case HYBRID = 'hybrid';
}
