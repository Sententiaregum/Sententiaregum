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

import MenuWrapper from '../../../components/app/MenuWrapper';
import Menu from '../../../components/app/Menu';
import TestUtils from 'react/lib/ReactTestUtils';
import React from 'react';
import chai from 'chai';
import ReactDOM from 'react-dom';

describe('MenuWrapper', () => {
  it('renders menu component as child near the brand', () => {
    const result = TestUtils.renderIntoDocument((
      <MenuWrapper>
        <Menu items={[]} />
      </MenuWrapper>
    ));

    const component = ReactDOM.findDOMNode(result);
    const bars      = component._childNodes[0]._childNodes[1];

    chai.expect(bars._childNodes).to.have.length(2);

    const brand = component._childNodes[0]._childNodes[0]._childNodes[0];

    chai.expect(brand._tagName).to.equal('a');
    chai.expect(brand._attributes.href._nodeValue).to.equal('/#/');
    chai.expect(brand._childNodes[0]._nodeValue).to.equal('Sententiaregum');
  });
});
