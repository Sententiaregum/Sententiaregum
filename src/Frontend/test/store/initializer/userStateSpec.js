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

import userState from '../../../store/initializer/userState';
import ApiKey from '../../../util/http/ApiKey';
import { stub } from 'sinon';
import { expect } from 'chai';

describe('userState', () => {
  it('builds the default state', () => {
    stub(ApiKey, 'getUsername', () => 'Ma27');
    stub(ApiKey, 'isLoggedIn', () => true);
    stub(ApiKey, 'isAdmin', () => true);
    stub(ApiKey, 'getApiKey', () => 'key');

    expect(userState()).to.deep.equal({
      auth: {
        is_admin:      true,
        apiKey:       'key',
        username:     'Ma27',
        authenticated: true,
        success:       true,
        message:       null
      },
      activation:   { success: false },
      creation:     {
        success:          false,
        errors:           {},
        name_suggestions: [],
        id:               null
      },
    });

    ApiKey.getUsername.restore();
    ApiKey.isLoggedIn.restore();
    ApiKey.isAdmin.restore();
    ApiKey.getApiKey.restore();
  });
});
