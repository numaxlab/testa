<?php

namespace Trafikrak\Models\Content;

enum TierType: string
{
    case RELATED_CONTENT_BANNER = 'related_content_banner';
    case RELATED_CONTENT_COLLECTION = 'related_content_collection';
    case BOOKSHOP_LATEST = 'bookshop_latest';
    case EDITORIAL_LATEST = 'editorial_latest';
    case EDUCATION_UPCOMING = 'education_upcoming';
    case EVENTS_UPCOMING = 'events_upcoming';
}
