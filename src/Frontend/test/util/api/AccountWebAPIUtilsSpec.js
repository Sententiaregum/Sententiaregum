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

import { expect } from 'chai';
import { spy, assert, stub } from 'sinon';
import AccountWebAPIUtils from '../../../util/api/AccountWebAPIUtils';
import moxios from 'moxios';
import ApiKey from '../../../util/http/ApiKeyService';

describe('AccountWebAPIUtils', () => {
  beforeEach(() => {
    moxios.install();
  });

  afterEach(() => {
    moxios.uninstall();
  });

  it('creates account', () => {
    const handler = spy(), testId  = Math.random(), response = { id: testId };
    moxios.stubRequest('/api/users.json', {
      status: 201,
      data:   response
    });

    AccountWebAPIUtils.createAccount({ username: 'Ma27' }, handler, function () {});
    moxios.wait(() => {
      assert.calledOnce(handler);
      expect(handler.calledWith(response)).to.equal(true);
    });
  });

  it('handles account errors', () => {
    const handler = spy(), response = { errors: { en: { property: ['Error'] } } };
    moxios.stubRequest('/api/users.json', {
      status: 201,
      data:   response
    });

    AccountWebAPIUtils.createAccount({ username: 'Ma27' }, function () {}, handler);
    moxios.wait(() => {
      expect(handler.called).to.equal(true);
      expect(handler.calledWith(response)).to.equal(true);
    })
  });

  it('activates user', () => {
    const response = { id: Math.random() }, handler = spy();
    moxios.stubRequest('/api/users/activate.json?username=Ma27&activation_key=foo_key', {
      status: 204,
      data:   response
    });

    AccountWebAPIUtils.activate('Ma27', 'foo_key', handler, function () {});
    moxios.wait(() => {
      assert.calledOnce(handler);
      expect(handler.calledWith());
    });
  });

  it('requests api keys', () => {
    moxios.stubRequest('/api/api-key.json', {
      status: 200,
      data:   {
        apiKey: 'key'
      }
    });

    const handler  = spy();
    const response = {
      apiKey:   'key',
      username: 'Ma27',
      roles:    [{ role: 'ROLE_USER' }]
    };
    moxios.stubRequest('/api/protected/users/credentials.json', {
      status: 200,
      data:   response
    });

    stub(ApiKey, 'addCredentials');

    AccountWebAPIUtils.requestApiKey('Ma27', '123456', handler, () => {});

    moxios.wait(() => {
      expect(spy.calledWith(response)).to.equal(true);
    });
    ApiKey.addCredentials.restore();
  });

  it('runs the logout request', () => {
    moxios.stubRequest('/api/api-key.json', {
      status: 200
    });

    const handler = spy();
    stub(ApiKey, 'purgeCredentials');

    AccountWebAPIUtils.logout(handler);

    moxios.wait(() => {
      expect(spy.calledOnce).to.equal(true);
      expect(ApiKey.purgeCredentials.calledOnce).to.equal(true);
      ApiKey.purgeCredentials.restore();
    });
  });
});
