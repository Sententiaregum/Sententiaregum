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

import { store, subscribe } from 'sententiaregum-flux-container';
import { GET_LOCALES }      from '../constants/Locale';
import { REQUEST_API_KEY }  from '../constants/Portal';
import Locale               from '../util/http/Locale';
import refreshLocaleOnLogin from './handler/refreshLocaleOnLogin';
import userStore            from './userStore';

export default store({
  [GET_LOCALES]:     subscribe(subscribe.chain()('locales')),
  [REQUEST_API_KEY]: subscribe(subscribe.chain()(refreshLocaleOnLogin), [userStore.getToken(REQUEST_API_KEY)])
}, { locales: {}, current: { locale: Locale.getLocale() } });
