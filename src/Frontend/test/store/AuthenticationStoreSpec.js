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

import AuthenticationStore from '../../store/AuthenticationStore';
import { expect } from 'chai';
import { runAction } from 'sententiaregum-flux-container';

describe('AuthenticationStore', () => {
  it('handles success', () => {
    runAction(() => {
      return dispatch => dispatch('REQUEST_API_KEY', {});
    }, []);

    const state = AuthenticationStore.getState();
    expect(Object.keys(state).length).to.equal(0);
  });

  it('stores login failure in case of login errors', () => {
    runAction(() => {
      return dispatch => dispatch('LOGIN_ERROR', { message: 'Credentials refused!' })
    }, []);

    const state = AuthenticationStore.getState();
    expect(Object.keys(state).length).to.equal(1);
    expect(state.message).to.equal('Credentials refused!');
  });
});
