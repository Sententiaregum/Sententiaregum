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

/**
 * State builder.
 *
 * @param {String} message The error message.
 *
 * @returns {Object} The next state.
 */
function state(message = null) {
  const hasMessage = !!message;
  return hasMessage ? { message } : {};
}

export default store({
  'REQUEST_API_KEY': {
    params:   [],
    function: state
  },
  'LOGIN_ERROR': {
    params:   ['message'],
    function: state
  }
}, {});
