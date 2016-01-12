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
import ReactDOM from 'react-dom';
import Menu from '../../../components/app/Menu';
import chai from 'chai';
import sinon from 'sinon';
import MenuActions from '../../../actions/MenuActions';
import MenuStore from '../../../store/MenuStore';
import TestUtils from 'react/lib/ReactTestUtils';

describe('Menu', () => {
  it('renders empty menu bar into document', () => {
    const result    = TestUtils.renderIntoDocument(<Menu items={[]} />);
    const component = ReactDOM.findDOMNode(result);

    chai.expect(component).to.equal(null);
  });

  it('renders menu items', () => {
    let clock  = sinon.useFakeTimers();
    let config = [
      {
        url: '/#/',
        label: 'menu.start'
      },
      {
        url: '/#/cmp',
        label: 'Test Component'
      }
    ];

    sinon.stub(MenuStore, 'getItems', () => config);

    const result = TestUtils.renderIntoDocument(<Menu items={[]} />);
    clock.tick(1000);
    const component = ReactDOM.findDOMNode(result);

    let items = component._childNodes;
    chai.expect(items).to.have.length(2);
    sinon.assert.called(MenuStore.getItems);

    let menuItem1 = items[0];
    let itemprop  = menuItem1._childNodes[0];
    chai.expect(itemprop._attributes.href._nodeValue).to.equal('/#/');
    chai.expect(itemprop._childNodes[0]._tagName).to.equal('span');
    chai.expect(itemprop._tagName).to.equal('a');
    chai.expect(itemprop._childNodes[0]._childNodes[0]._nodeValue).to.equal('Homepage');
    chai.expect();

    MenuStore.getItems.restore();
    clock.restore();
  });
});
