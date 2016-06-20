/*
 * This file is part of the Sententiaregum project.
 *
 * (c) Maximilian Bosch <maximilian.bosch.27@gmail.com>
 * (c) Ben Bieler <benjaminbieler2014@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

'use strict';

import { store } from 'sententiaregum-flux-container';
import Locale from '../util/http/LocaleService';
import handleLogin from './handler/handleLogin';

export default store({
  'CHANGE_LOCALE': {
    params: ['locale']
  },
  'REQUEST_API_KEY': {
    function: handleLogin
  }
}, { locale: Locale.getLocale() });
