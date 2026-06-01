<?php

namespace Testa\Policies;

use Illuminate\Foundation\Auth\User;
use Testa\Models\Education\Course;
use Testa\Models\Education\CourseModule;
use Testa\Models\Media\Media;
use Testa\Models\Media\Visibility;
use Testa\Models\Membership\Benefit;
use Testa\Storefront\Queries\Membership\CustomerHasActiveBenefit;

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

        if ($media->visibility === Visibility::MEMBERS_ONLY) {
            $customer = $user->latestCustomer();

            if (! $customer) {
                return false;
            }

            return new CustomerHasActiveBenefit()->execute($customer, Benefit::PRIVATE_MEDIA_ACCESS);
        }

        // PRIVATE: access only if customer purchased related course
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
