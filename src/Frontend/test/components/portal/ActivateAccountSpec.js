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

import ActivateAccount from '../../../components/portal/ActivateAccount';
import TestUtils from 'react/lib/ReactTestUtils';
import React from 'react';
import chai from 'chai';
import sinon from 'sinon';
import AccountWebAPIUtils from '../../../util/api/AccountWebAPIUtils';
import ReactDOM from 'react-dom';

describe('ActivateAccount', () => {
  it('activates user accounts', () => {
    sinon.stub(AccountWebAPIUtils, 'activate', (name, key, success, error) => {
      error();
    });

    let timer = sinon.useFakeTimers();
    const cmp  = TestUtils.renderIntoDocument(<ActivateAccount params={{ name: 'Ma27', key: Math.random() }} />);

    timer.tick(1000);
    const node = ReactDOM.findDOMNode(cmp);
    chai.expect(node._childNodes[1]._attributes.class._nodeValue).to.equal('alert alert-danger alert-dismissable');

    timer.restore();
    AccountWebAPIUtils.activate.restore();
  });

  it('handles activation failures', () => {
    sinon.stub(AccountWebAPIUtils, 'activate', (name, key, success, error) => {
      success();
    });

    let timer = sinon.useFakeTimers();
    const cmp  = TestUtils.renderIntoDocument(<ActivateAccount params={{ name: 'Ma27', key: Math.random() }} />);

    timer.tick(1000);
    const node = ReactDOM.findDOMNode(cmp);
    chai.expect(node._childNodes[1]._attributes.class._nodeValue).to.equal('alert alert-success alert-dismissable');

    timer.restore();
    AccountWebAPIUtils.activate.restore();
  });
});
