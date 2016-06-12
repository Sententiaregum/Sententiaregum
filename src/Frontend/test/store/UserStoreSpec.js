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

import UserStore from '../../store/UserStore';
import { runAction } from 'sententiaregum-flux-container';
import { expect } from 'chai';
import ApiKey from '../../util/http/ApiKeyService';
import { stub } from 'sinon';

describe('UserStore', () => {
  it('receives new data', () => {
    stub(ApiKey, 'getUsername', () => 'Ma27');
    stub(ApiKey, 'isLoggedIn', () => true);
    stub(ApiKey, 'isAdmin', () => true);
    stub(ApiKey, 'getApiKey', () => 'key');

    ['REQUEST_API_KEY', 'LOGOUT'].forEach(event => {
      runAction(() => {
        return dispatch => dispatch(event, {});
      }, []);

      const state = UserStore.getState();

      expect(state.is_admin).to.equal(true);
      expect(state.is_logged_in).to.equal(true);
      expect(state.username).to.equal('Ma27');
      expect(state.key).to.equal('key');
    });

    ApiKey.getUsername.restore();
    ApiKey.isLoggedIn.restore();
    ApiKey.isAdmin.restore();
    ApiKey.getApiKey.restore();
  });
});
