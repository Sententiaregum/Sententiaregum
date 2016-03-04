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
import initializeCredentials from './initializer/initializeCredentials';

export default store({
  'REQUEST_API_KEY': {
    params: [],

    // in this case the initializer can be used for state changes since
    // the procedure of fetching data from the `ApiKey` service
    // is alwasy the same.
    function: initializeCredentials
  }
}, initializeCredentials);
