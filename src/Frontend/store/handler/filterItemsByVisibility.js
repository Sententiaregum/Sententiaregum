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

/**
 * Custom store handler which filters items by some auth configuration.
 *
 * @param {Array}  items    The menu items.
 * @param {Object} authData Information about the authentication status.
 *
 * @returns {Array} The new state.
 */
export default (items, authData) => {
  return items.filter(item => {
    return !(
      'ROLE_ADMIN' === item.role && !authData.is_admin
      || item.logged_in && !authData.logged_in
      || item.portal && authData.logged_in
    );
  });
};
