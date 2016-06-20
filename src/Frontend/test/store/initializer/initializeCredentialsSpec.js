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

import initializeCredentials from '../../../store/initializer/initializeCredentials';
import ApiKey from '../../../util/http/ApiKeyService';
import { stub } from 'sinon';
import { expect } from 'chai';

describe('initializeCredentials', () => {
  it('builds user state', () => {
    stub(ApiKey, 'getUsername', () => 'Ma27');
    stub(ApiKey, 'isLoggedIn', () => true);
    stub(ApiKey, 'isAdmin', () => true);
    stub(ApiKey, 'getApiKey', () => 'key');

    const nextState = initializeCredentials();
    expect(nextState.is_admin).to.equal(true);
    expect(nextState.is_logged_in).to.equal(true);
    expect(nextState.username).to.equal('Ma27');
    expect(nextState.key).to.equal('key');

    ApiKey.getUsername.restore();
    ApiKey.isLoggedIn.restore();
    ApiKey.isAdmin.restore();
    ApiKey.getApiKey.restore();
  });
});
