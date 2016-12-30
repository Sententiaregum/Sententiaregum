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

import { protectApp, guardFromPortal }  from '../../../util/security/appProfileChecker';
import { stub, spy }                    from 'sinon';
import { expect }                       from 'chai';
import store                            from '../../../config/redux/store';

describe('appProfileChecker', () => {
  it('redirects to login unless authenticated', () => {
    stub(store, 'getState', () => ({ user: { security: { authenticated: false } } }));

    const replacer = spy();
    protectApp({}, replacer);

    expect(replacer.calledOnce).to.equal(true);
    expect(replacer.calledWith({ pathname: '/' })).to.equal(true);

    store.getState.restore();
  });

  it('redirects from portal (login/register) to dashboard if authenticated', () => {
    stub(store, 'getState', () => ({ user: { security: { authenticated: true } } }));

    const replacer = spy();
    guardFromPortal({}, replacer);

    expect(replacer.calledOnce).to.equal(true);
    expect(replacer.calledWith({ pathname: '/dashboard' })).to.equal(true);

    store.getState.restore();
  });

  it('resumes app flow if authenticated', () => {
    stub(store, 'getState', () => ({ user: { security: { authenticated: true } } }));

    const replacer = spy();
    protectApp({}, replacer);

    expect(replacer.called).to.equal(false);

    store.getState.restore();
  });
});
