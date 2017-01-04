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

import store from '../../config/redux/store';

const selectSecurity = store => store.getState().user.security;

const routeChangeHandler = (ensureAuthenticated = true, redirectURL = '/') => (n, r) => {
  const { authenticated } = selectSecurity(store);
  if (authenticated !== ensureAuthenticated) {
    r({ pathname: redirectURL });
  }
};

export const protectApp      = routeChangeHandler();
export const guardFromPortal = routeChangeHandler(false, '/dashboard');
