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

import userStore from '../../store/userStore';

/**
 * Replace handler for locations in the app.
 *
 * @param {boolean}  needsLogin Whether the page needs login or not.
 * @param {String}   target     Target url.
 * @param {Function} replace    The function to replace state.
 *
 * @returns {void}
 */
function replace(needsLogin, target, replace) {
  const isLoggedIn = userStore.getStateValue('auth.authenticated');
  if (needsLogin === isLoggedIn) {
    replace({
      pathname: target
    });
  }
}

/**
 * Simple router hook to protect certain pages.
 *
 * @param {Object}   nextState Next state.
 * @param {Function} replacer  Location handler.
 *
 * @returns {void}
 */
export function protectPage(nextState, replacer) {
  replace(false, '/', replacer);
}

/**
 * Router hook which redirects to the dashboard if logged in.
 *
 * @param {Object}   nextState Next state.
 * @param {Function} replacer  Location handler.
 *
 * @returns {void}
 */
export function redirectToDashboard(nextState, replacer) {
  replace(true, '/dashboard', replacer);
}
