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
import TestUtils from 'react/lib/ReactTestUtils';
import DismissableAlertBox from '../../../../components/app/markup/DismissableAlertBox';
import ReactDOM from 'react-dom';
import chai from 'chai';

describe('DismissableAlertBox', () => {
  it('renders a dismissable alert box', () => {
    const cmp  = TestUtils.renderIntoDocument(<DismissableAlertBox bsStyle="success">Content</DismissableAlertBox>);
    const node = ReactDOM.findDOMNode(cmp);

    chai.expect(node._attributes.class._nodeValue).to.equal('alert alert-success alert-dismissable');
    chai.expect(node._childNodes[1]._childNodes[0]._nodeValue).to.equal('Content');

    TestUtils.Simulate.click(ReactDOM.findDOMNode(cmp).getElementsByClassName('sr-only')[0]);
    const reloaded = ReactDOM.findDOMNode(cmp);
    chai.expect(reloaded).to.equal(null);
  });
});
