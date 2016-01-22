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

import React from 'react';
import Success from '../../../../components/portal/signup/Success';
import TestUtils from 'react/lib/ReactTestUtils';
import ReactDOM from 'react-dom';
import chai from 'chai';

describe('Success', () => {
  it('renders success box', () => {
    const cmp  = TestUtils.renderIntoDocument(<Success />);
    const node = ReactDOM.findDOMNode(cmp.refs.textbox);

    chai.expect(node._childNodes[0]._nodeValue).to.equal('The account has been created successfully. You can now activate your account using the activation email.');
    chai.expect(ReactDOM.findDOMNode(cmp)._attributes.class._nodeValue).to.equal('alert alert-success alert-dismissable');
  });
});
