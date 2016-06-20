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
import Locale from '../../../util/http/LocaleService';
import { stub } from 'sinon';

describe('ApiKeyService', () => {
  afterEach(() => {
    localStorage.removeItem('api_key');
    localStorage.removeItem('username');
    localStorage.removeItem('user_roles');
  });

  it('checks against empty data', () => {
    expect(ApiKeyService.isLoggedIn()).to.equal(false);
    expect(ApiKeyService.getApiKey()).to.equal(null);
    expect(ApiKeyService.isAdmin()).to.equal(false);
  });

  it('adds credentials', () => {
    stub(Locale, 'setLocale');
    const data = {
      api_key: 'key',
      roles:    { ROLE_USER: { role: 'ROLE_USER' }, ROLE_ADMIN: { role: 'ROLE_ADMIN' } },
      username: 'Ma27',
      locale:   'en'
    };

    ApiKeyService.addCredentials(data);
    expect(ApiKeyService.isLoggedIn()).to.equal(true);
    expect(ApiKeyService.getApiKey()).to.equal('key');
    expect(ApiKeyService.isAdmin()).to.equal(true);
    expect(ApiKeyService.getUsername()).to.equal('Ma27');
    expect(Locale.setLocale.calledWith('en')).to.equal(true);

    Locale.setLocale.restore();
  });

  it('purges credentials', () => {
    localStorage.setItem('api_key', 'key');
    localStorage.setItem('roles', '["ROLE_USER"]');
    localStorage.setItem('username', 'Ma27');
    ApiKeyService.purgeCredentials();

    expect(ApiKeyService.isAdmin()).to.equal(false);
  });
});
