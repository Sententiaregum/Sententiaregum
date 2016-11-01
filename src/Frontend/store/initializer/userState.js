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

import ApiKey from '../../util/http/ApiKey';

/**
 * Simple initialization callback which creates the basic user state.
 *
 * @returns {Object} The basic user state.
 */
export default () => ({
  activation: { success: false },
  creation:   {
    success:          false,
    errors:           {},
    name_suggestions: [],
    id:               null
  },
  auth: {
    is_admin:      ApiKey.isAdmin(),
    apiKey:        ApiKey.getApiKey(),
    username:      ApiKey.getUsername(),
    authenticated: ApiKey.isLoggedIn(),
    success:       ApiKey.isLoggedIn(),
    message:       null
  }
});
