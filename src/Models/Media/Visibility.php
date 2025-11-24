<?php

namespace Trafikrak\Models\Media;

enum Visibility: string
{
    case PUBLIC = 'public';
    case PRIVATE = 'private';
}
