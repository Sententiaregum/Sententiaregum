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

import ApiKey from '../../util/http/ApiKeyService';

/**
 * Handler which initializes the user store.
 *
 * @returns {Object} The new state.
 */
export default () => {
  return {
    is_admin:     ApiKey.isAdmin(),
    key:          ApiKey.getApiKey(),
    is_logged_in: ApiKey.isLoggedIn(),
    username:     ApiKey.getUsername()
  };
};
