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

import MenuActions from '../../actions/MenuActions';
import sinon from 'sinon';
import dispatcher from '../../dispatcher/AppDispatcher';
import chai from 'chai';
import {ApiKey} from '../../util/http/facade/HttpServices';
import Cookies from 'cookies-js';

describe('MenuActions', () => {
  it('publishes menu items in order to transform them', () => {
    sinon.createStubInstance(Cookies);
    sinon.stub(ApiKey, 'isLoggedIn', () => false);

    sinon.stub(dispatcher, 'dispatch', (eventPayload) => {
      chai.expect(eventPayload.items).to.have.length(2);
      chai.expect(eventPayload.event).to.equal('TRANSFORM_ITEMS');
      chai.expect(eventPayload.authData.logged_in).to.equal(false);
      chai.expect(eventPayload.authData.is_admin).to.equal(false);
    });

    MenuActions.buildMenuItems(
      [
        {
          url: '/#/',
          label: 'Start'
        },
        {
          url: '/#/admin',
          label: 'Admin',
          role: 'ROLE_ADMIN',
          logged_in: true
        }
      ]
    );

    sinon.assert.calledOnce(dispatcher.dispatch);

    dispatcher.dispatch.restore();
    ApiKey.isLoggedIn.restore();
  });
});
