<?php

namespace Testa\Models\Media;

enum Visibility: string
{
    case PUBLIC = 'public';
    case PRIVATE = 'private';
    case MEMBERS_ONLY = 'members_only';
}
