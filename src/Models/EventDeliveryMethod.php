<?php

namespace Testa\Models;

enum EventDeliveryMethod: string
{
    case IN_PERSON = 'in_person';
    case ONLINE = 'online';
    case HYBRID = 'hybrid';
    case MOOC = 'mooc';
}
