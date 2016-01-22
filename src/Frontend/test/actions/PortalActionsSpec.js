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

import PortalActions from '../../actions/PortalActions';
import sinon from 'sinon';
import AppDispatcher from '../../dispatcher/AppDispatcher';
import chai from 'chai'
import AccountWebAPIUtils from '../../util/api/AccountWebAPIUtils';
import Portal  from '../../constants/Portal';

describe('PortalActions', () => {
  it('triggers the registration process', () => {
    sinon.stub(AccountWebAPIUtils, 'createAccount', (data, success) => {
      chai.expect(data.username).to.equal('Ma27');
      success.apply(null, [{ id: Math.random() }]);
    });

    sinon.stub(AppDispatcher, 'dispatch');
    PortalActions.registration({ username: 'Ma27' });

    sinon.assert.calledOnce(AppDispatcher.dispatch);
    AppDispatcher.dispatch.restore();
    AccountWebAPIUtils.createAccount.restore();
  });

  it('handles registration errors', () => {
    sinon.stub(AccountWebAPIUtils, 'createAccount', (data, success, error) => {
      error.apply(null, [{ errors: [], name_suggestoins: [] }]);
    });

    sinon.stub(AppDispatcher, 'dispatch', (payload) => {
      chai.expect(payload.event).to.equal(Portal.ACCOUNT_VALIDATION_ERROR);
      chai.expect(typeof payload.errors).to.not.equal('undefined');
      chai.expect(typeof payload.nameSuggestions).to.not.equal('undefined');
    });
    PortalActions.registration({ username: 'Ma27' });

    sinon.assert.calledOnce(AppDispatcher.dispatch);
    AppDispatcher.dispatch.restore();
    AccountWebAPIUtils.createAccount.restore();
  });

  it('activates users', () => {
    sinon.stub(AccountWebAPIUtils, 'activate', (name, key, success) => {
      chai.expect(name).to.equal('Ma27');
      chai.expect(key).to.equal('foo');
      success();
    });

    sinon.stub(AppDispatcher, 'dispatch');
    PortalActions.activate('Ma27', 'foo');

    sinon.assert.calledOnce(AppDispatcher.dispatch);
    AppDispatcher.dispatch.restore();
    AccountWebAPIUtils.activate.restore();
  });
});
