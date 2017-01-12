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

/**
 * Custom store handler which filters items by some auth configuration.
 *
 * @param {Array}  items    The menu items.
 * @param {Object} authData Information about the authentication status.
 *
 * @returns {Object} The new state.
 */
export default ({ items, authData }) => {
  return {
    items: items.filter(item => {
      return !(
        'ROLE_ADMIN' === item.role && !authData.is_admin
        || item.logged_in && !authData.logged_in
        || item.portal && authData.logged_in
      );
    })
  };
};
