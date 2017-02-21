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

import { store, subscribe }                                          from 'sententiaregum-flux-container';
import userState                                                     from './initializer/userState';
import { REQUEST_API_KEY, LOGOUT, CREATE_ACCOUNT, ACTIVATE_ACCOUNT } from '../constants/Portal';

export default store({
  [REQUEST_API_KEY]:  subscribe(subscribe.chain()('auth')),
  [LOGOUT]:           subscribe(subscribe.chain()('auth')),
  [CREATE_ACCOUNT]:   subscribe(subscribe.chain()('creation')),
  [ACTIVATE_ACCOUNT]: subscribe(subscribe.chain()('activation'))
}, userState);
