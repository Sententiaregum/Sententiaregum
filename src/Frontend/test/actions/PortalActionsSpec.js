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

import { registration, activate, authenticate } from '../../actions/PortalActions';
import { stub, assert } from 'sinon';
import { expect } from 'chai'
import AccountWebAPIUtils from '../../util/api/AccountWebAPIUtils';
import RegistrationStore from '../../store/RegistrationStore';
import { runAction } from 'sententiaregum-flux-container';
import ActivationStore from '../../store/ActivationStore';
import AuthenticationStore from '../../store/AuthenticationStore';

describe('PortalActions', () => {
  it('triggers the registration process', () => {
    stub(AccountWebAPIUtils, 'createAccount', (data, success) => {
      expect(data.username).to.equal('Ma27');
      success.apply(null, [{ id: Math.random() }]);
    });

    runAction(registration, [{ username: 'Ma27' }]);

    AccountWebAPIUtils.createAccount.restore();
    expect(RegistrationStore.getState()).to.equal(null); // when the state is null, the registration was successful
  });

  it('handles registration errors', () => {
    stub(AccountWebAPIUtils, 'createAccount', (data, success, error) => {
      error.apply(null, [{ errors: { username: ['Error!'] }, name_suggestions: [] }]);
    });

    runAction(registration, [{ username: 'Ma27' }]);
    expect(RegistrationStore.getState().errors['username'].length).to.equal(1);
    expect(RegistrationStore.getState().suggestions.length).to.equal(0);

    AccountWebAPIUtils.createAccount.restore();
  });

  it('activates users', () => {
    stub(AccountWebAPIUtils, 'activate', (name, key, success) => {
      expect(name).to.equal('Ma27');
      expect(key).to.equal('foo');
      success();
    });

    runAction(activate, ['Ma27', 'foo']);
    expect(ActivationStore.getState().success).to.equal(true);

    AccountWebAPIUtils.activate.restore();
  });

  it('causes an invalid state if the activation fails', () => {
    stub(AccountWebAPIUtils, 'activate', (name, key, success, error) => {
      expect(name).to.equal('Ma27');
      expect(key).to.equal('foo');
      error();
    });

    runAction(activate, ['Ma27', 'foo']);
    expect(ActivationStore.getState().success).to.equal(false);

    AccountWebAPIUtils.activate.restore();
  });

  it('authenticates users', () => {
    stub(AccountWebAPIUtils, 'requestApiKey', (username, password, error) => {
      error({ message: 'Credential error!' })
    });

    runAction(authenticate, ['Ma27', '123456']);
    expect(AuthenticationStore.getState().message).to.equal('Credential error!');

    AccountWebAPIUtils.requestApiKey.restore();
  });
});
