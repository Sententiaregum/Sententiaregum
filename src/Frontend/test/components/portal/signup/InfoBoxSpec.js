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
import InfoBox from '../../../../components/portal/signup/InfoBox';
import TestUtils from 'react/lib/ReactTestUtils';
import ReactDOM from 'react-dom';
import chai from 'chai';

describe('InfoBox', () => {
  it('renders information for registration page', () => {
    const cmp  = TestUtils.renderIntoDocument(<InfoBox />);
    const node = ReactDOM.findDOMNode(cmp.refs.textbox);

    chai.expect(node._childNodes[0]._nodeValue).to.equal('Please fill all these fields. After that you\'ll get an activation email in order to activate your account.');
    chai.expect(ReactDOM.findDOMNode(cmp)._attributes.class._nodeValue).to.equal('alert alert-info alert-dismissable');
  });
});
