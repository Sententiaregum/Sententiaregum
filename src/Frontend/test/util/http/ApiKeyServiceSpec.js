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

import ApiKeyService from '../../../util/http/ApiKeyService';
import { expect } from 'chai';

describe('ApiKeyService', () => {
  it('checks against empty data', () => {
    localStorage.removeItem('api_key');
    localStorage.removeItem('username');
    localStorage.removeItem('user_roles');

    expect(ApiKeyService.isLoggedIn()).to.equal(false);
    expect(ApiKeyService.getApiKey()).to.equal(null);
    expect(ApiKeyService.isAdmin()).to.equal(false);
  });

  it('adds credentials', () => {
    const data = {
      apiKey:   'key',
      roles:    [{ role: 'ROLE_USER' }, { role: 'ROLE_ADMIN' }],
      username: 'Ma27'
    };

    ApiKeyService.addCredentials(data);
    expect(ApiKeyService.isLoggedIn()).to.equal(true);
    expect(ApiKeyService.getApiKey()).to.equal('key');
    expect(ApiKeyService.isAdmin()).to.equal(true);
    expect(ApiKeyService.getUsername()).to.equal('Ma27');
  });
});
