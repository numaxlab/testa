<?php

namespace Testa\Models\Content;

enum TierType: string
{
    case RELATED_CONTENT_BANNER = 'related_content_banner';
    case RELATED_CONTENT_COLLECTION = 'related_content_collection';
    case RELATED_CONTENT_COURSE = 'related_content_course';
    case RELATED_CONTENT_EDUCATION_TOPIC = 'related_content_education_topic';
    case RELATED_CONTENT_MEDIA = 'related_content_media';
    case EDITORIAL_LATEST = 'editorial_latest';
    case EDUCATION_UPCOMING = 'education_upcoming';
    case MOOC = 'mooc';
    case EVENTS_UPCOMING = 'events_upcoming';
    case ARTICLES_LATEST = 'articles_latest';
}
