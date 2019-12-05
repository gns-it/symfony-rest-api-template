<?php
/**
 *  * Created by PhpStorm.
 * User: sergey_h
 * Date: 23.11.18
 * Time: 9:06
 */

namespace App\Entity\Extra;


class Groups
{
    /**
     * @var array
     */
    const DETAILED_SHORT = [self::SHORT, self::DETAILED];

    /**
     * @var array
     */
    const SHORT = 'Short';

    /**
     * @var array
     */
    const EXTRA_SHORT = 'Extra_ahort';

    /**
     * @var array
     */
    const UUID =  "Uuid";

    /**
     * @var string
     */
    const DETAILED = 'Detailed';

    /**
     * @var string
     */
    const CREATOR = 'Creator';

    /**
     * @var string
     */
    const LOCATION = 'Location';

    /**
     * @var string
     */
    const TITLES = 'Titles';

    /**
     * @var string
     */
    const CUSTOM = 'Custom';

    /**
     * @var string
     */
    const TIMESTAMPS = 'timestamps';

    /**
     * @var string
     */
    const TRANSACTIONS = 'transactions';

    /**
     * @var string
     */
    const TRANSACTIONS_DETAILED = 'transactions_detailed';

    /**
     * @var string
     */
    const WALLET = 'wallet';

    /**
     * @var string
     */
    const WALLET_TRANSACTIONS = [self::WALLET,self::TRANSACTIONS];

    /**
     * @var string
     */
    const PROFILE_DETAILED = [self::DETAILED, self::SHORT, self::PROFILE ];

    /**
     * @var string
     */
    const PROFILE = 'Profile';

    /** @var string */
    const FOLLOWER = 'Follower';

    /** @var string */
    const FOLLOWING = 'Following';

    /** @var string */
    const UPCOMING_EVENTS = 'Upcoming_events';

    const MEDIA = 'Media';


}