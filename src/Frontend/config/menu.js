/*
 * This file is part of the Sententiaregum project.
 *
 * (c) Maximilian Bosch <maximilian@mbosch.me>
 * (c) Ben Bieler <ben@benbieler.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

'use strict';

export default [
  {
    label:  'menu.start',
    url:    '/#/',
    portal: true
  }, {
    label:  'pages.portal.head',
    url:    '/#/sign-up',
    portal: true
  }, {
    label:     'pages.network.dashboard.index.title',
    url:       '/#/dashboard',
    logged_in: true
  }, {
    label:     'pages.network.logout',
    url:       '/#/logout',
    logged_in: true
  }
];
