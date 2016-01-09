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

import ReactDOM from 'react-dom';
import TestUtils from 'react/lib/ReactTestUtils';
import chai from 'chai';
import DropDownItem from '../../../../components/app/markup/DropDownItem';
import React from 'react';
import NavDropdown from 'react-bootstrap/lib/NavDropdown';

describe('DropDownItem', () => {
  it('renders a dropdown item including properties', () => {
    const result = TestUtils.renderIntoDocument((
      <NavDropdown id="test-dropdown" title="Test">
        <DropDownItem
          key="test"
          isActive={true}
          id="test"
          displayName="Test"
        />
      </NavDropdown>
    ));

    const component = ReactDOM.findDOMNode(result);
    const dropdown  = component._childNodes[1]._childNodes[0];

    chai.expect(dropdown._attributes.class._nodeValue).to.equal('active');
    chai.expect(dropdown._childNodes[0]._childNodes[0]._nodeValue).to.equal('Test');
  });
});
