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

import AppDispatcher from '../../dispatcher/AppDispatcher';
import RegistrationStore from '../../store/RegistrationStore';
import Portal from '../../constants/Portal';
import sinon from 'sinon';
import chai from 'chai';

describe('RegistrationStore', () => {
  it('handles success', () => {
    sinon.stub(RegistrationStore, 'emitChange', (eventName) => {
      chai.expect(eventName).to.equal('CreateAccount.Success');
    });

    AppDispatcher.dispatch({
      event: Portal.CREATE_ACCOUNT
    });

    sinon.assert.calledOnce(RegistrationStore.emitChange);
    chai.expect(Object.keys(RegistrationStore.getErrors()).length).to.equal(0);

    RegistrationStore.emitChange.restore();
  });

  it('stores validation errors', () => {
    sinon.stub(RegistrationStore, 'emitChange', (eventName) => {
      chai.expect(eventName).to.equal('CreateAccount.Error');
    });

    const errors = [
      { username: ['Username already in use!'] },
      { password: ['Password cannot be empty!'] }
    ];

    AppDispatcher.dispatch({
      event:  Portal.ACCOUNT_VALIDATION_ERROR,
      errors,
      nameSuggestions: ['Ma27_2016']
    });

    sinon.assert.calledOnce(RegistrationStore.emitChange);
    chai.expect(Object.keys(RegistrationStore.getErrors()).length).to.equal(2);
    chai.expect(RegistrationStore.getErrors()).to.equal(errors);
    chai.expect(RegistrationStore.getSuggestions()[0]).to.equal('Ma27_2016');

    RegistrationStore.emitChange.restore();
  });
});
