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

import userActions from '../../actions/userActions';
import { stub, assert } from 'sinon';
import { expect } from 'chai'
import userStore from '../../store/userStore';
import TestUtils from 'sententiaregum-flux-container/lib/testing/TestUtils';
import axios from 'axios';
import { CREATE_ACCOUNT, ACTIVATE_ACCOUNT, LOGOUT, REQUEST_API_KEY } from '../../constants/Portal';
import promise from '../fixtures/promise';
import ApiKey from '../../util/http/ApiKey';

describe('userActions', () => {
  it('triggers the registration process', () => {
    const apiStub = stub(axios, 'post', promise(true, { data: { id: '38a5e576-a857-44e4-b85e-fc32546d3c1a' } }));

    TestUtils.executeAction(userActions, CREATE_ACCOUNT, [
      { username: 'Ma27', password: '123456', email: 'Ma27@sententiaregum.dev', locale: 'en' }
    ])({ success: true, id: '38a5e576-a857-44e4-b85e-fc32546d3c1a', name_suggestions: [], errors: {} });

    expect(axios.post.calledWith('/api/users.json', { username: 'Ma27', password: '123456', email: 'Ma27@sententiaregum.dev', locale: 'en' })).to.equal(true);
    axios.post.restore();
  });

  it('handles registration errors', () => {
    stub(axios, 'post', promise(false, { data: { errors: { username: { en: 'Invalid name given!' } } } }));

    TestUtils.executeAction(userActions, CREATE_ACCOUNT, [
      { username: 'Ma27', password: '123456', email: 'Ma27@sententiaregum.dev', locale: 'en' }
    ])({ success: false, errors: { username: { en: 'Invalid name given!' } }, name_suggestions: [], id: null });

    expect(axios.post.calledWith('/api/users.json', { username: 'Ma27', password: '123456', email: 'Ma27@sententiaregum.dev', locale: 'en' })).to.equal(true);
    axios.post.restore();
  });

  it('activates users', () => {
    stub(axios, 'patch', promise(true, {}));

    TestUtils.executeAction(userActions, ACTIVATE_ACCOUNT, [{ username: 'Ma27', key: 'ZUx$3d1wX{!hXNq' }])({ success: true });

    expect(axios.patch.calledWith('/api/users/activate.json?username=Ma27&activation_key=ZUx$3d1wX{!hXNq', {})).to.equal(true);
    axios.patch.restore();
  });

  it('causes an invalid state if the activation fails', () => {
    const apiStub = stub(axios, 'patch', promise(false, {}));

    TestUtils.executeAction(userActions, ACTIVATE_ACCOUNT, [{ username: 'Ma27', key: 'ZUx$3d1wX{!hXNq' }])({ success: false });

    expect(axios.patch.calledWith('/api/users/activate.json?username=Ma27&activation_key=ZUx$3d1wX{!hXNq', {})).to.equal(true);
    axios.patch.restore();
  });

  it('authenticates users', () => {
    const apiKey = 'eae819fe-ed8b-4228-8ba5-13b2399ca79c';

    const data = { username: 'Ma27', apiKey, locale: 'en', roles: [{ role: 'ROLE_USER' }, { role: 'ROLE_ADMIN' }] };

    stub(ApiKey, 'addCredentials');
    stub(ApiKey, 'isAdmin', () => true);
    stub(axios, 'post', promise(true, { data: { apiKey } }));
    stub(axios, 'get', promise(true, { data }));

    TestUtils.executeAction(userActions, REQUEST_API_KEY, [{ username: 'Ma27', password: '123456' }])({
      username:      'Ma27',
      apiKey,
      locale:        'en',
      success:       true,
      authenticated: true,
      is_admin:      true
    });

    expect(ApiKey.addCredentials.calledWith(data)).to.equal(true);
    expect(axios.post.calledWith('/api/api-key.json', { login: 'Ma27', password: '123456' })).to.equal(true);

    axios.post.restore();
    axios.get.restore();
    ApiKey.addCredentials.restore();
    ApiKey.isAdmin.restore();
  });

  it('runs the logout process', () => {
    stub(ApiKey, 'getApiKey', () => 'ZUx$3d1wX{!hXNq');

    const apiStub = stub(axios, 'delete', promise(true, {}));

    TestUtils.executeAction(userActions, LOGOUT, [])({ authenticated: false, success: false });
    expect(axios.delete.calledWith('/api/api-key.json', { headers: { 'X-API-KEY': 'ZUx$3d1wX{!hXNq' } })).to.equal(true);

    axios.delete.restore();
    ApiKey.getApiKey.restore();
  });
});
