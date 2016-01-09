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
import LoadingDropDown from '../../../../components/app/markup/LoadingDropDown';
import React from 'react';
import NavDropdown from 'react-bootstrap/lib/NavDropdown';

describe('LoadingDropDown', () => {
  it('renders a loading dropdown', () => {
    const result = TestUtils.renderIntoDocument((
      <NavDropdown id="test-dropdown" title="Test">
        <LoadingDropDown translationContent="menu.l10n_loading" />
      </NavDropdown>
    ));

    const component = ReactDOM.findDOMNode(result);
    const dropdown  = component._childNodes[1]._childNodes[0];

    chai.expect(dropdown._childNodes[0]._childNodes[0]._childNodes[0]._childNodes[0]._nodeValue).to.equal('Loading languages...');

    const span = dropdown._childNodes[0]._childNodes[0];
    chai.expect(span._tagName).to.equal('span');
    chai.expect(span._attributes.class._nodeValue).to.equal('loading');
  });
});
 