/**
 * Copyright 2020 aheadWorks. All rights reserved.\nSee LICENSE.txt for license details.
 */

define([
    'jquery',
    'calendar'
], function ($, calendar) {
    'use strict';

    $.widget('mage.awCtqCalendar', calendar.calendar, {
        dateTimeFormat: {
            date: {
                'yy': 'y'
            }
        }
    });

    return $.mage.awCtqCalendar;
});
