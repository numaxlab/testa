<?php

namespace Trafikrak\Policies;

use Illuminate\Foundation\Auth\User;
use Trafikrak\Models\Education\Course;
use Trafikrak\Models\Education\CourseModule;
use Trafikrak\Models\Media\Media;
use Trafikrak\Models\Media\Visibility;

class MediaPolicy
{
    public function view(?User $user, Media $media): bool
    {
        if ($media->visibility === Visibility::PUBLIC) {
            return true;
        }

        if (! $user) {
            return false;
        }

        $customer = $user->latestCustomer();

        foreach ($media->attachments as $attachment) {
            if ($attachment->attachable instanceof Course) {
                if ($customer->courses->contains($attachment->attachable)) {
                    return true;
                }
            } elseif ($attachment->attachable instanceof CourseModule) {
                if ($customer->courses->contains($attachment->attachable->course)) {
                    return true;
                }
            }
        }

        return false;
    }
}
