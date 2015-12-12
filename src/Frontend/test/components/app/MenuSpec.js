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
import Menu from '../../../components/app/Menu';
import chai from 'chai';
import sinon from 'sinon';
import MenuActions from '../../../actions/MenuActions';
import MenuStore from '../../../store/MenuStore';
import TestUtils from 'react/lib/ReactTestUtils';

describe('Menu', () => {
  it('renders empty menu bar into document', () => {
    const MenuComponent = new Menu({items:[]});
    const component     = MenuComponent.render();

    chai.expect(component._store.props.children[2]).to.be.undefined;

    let localeSwitcher = component._store.props.children[1];
    chai.expect(localeSwitcher).not.to.be.undefined;
  });

  it('renders menu items', () => {
    let config = [
      {
        url: '/#/',
        label: 'Start'
      },
      {
        url: '/#/cmp',
        label: 'Test Component'
      }
    ];

    const MenuComponent = new Menu({items:[]});
    sinon.stub(MenuActions, 'buildMenuItems');
    sinon.stub(MenuStore, 'getItems', () => config);

    MenuComponent.componentDidMount();

    sinon.stub(MenuComponent, 'setState', (change) => {
      MenuComponent.state.items = change.items;
    });
    MenuComponent.storeMenuItems(); // simulate refreshing

    const result = MenuComponent.render();

    let item = result._store.props.children[2]._store.props.children;
    chai.expect(item).to.have.length(2);
    sinon.assert.calledOnce(MenuActions.buildMenuItems);
    sinon.assert.calledOnce(MenuStore.getItems);

    for (let menuItem of item) {
      chai.expect(menuItem._store.props.key).to.equal(menuItem._store.props.children._store.props.children);
    }

    let menuItem1 = item[0];
    let itemprop  = menuItem1._store.props.children._store.props;
    chai.expect(itemprop.content).to.equal('Start');

    MenuStore.getItems.restore();
    MenuActions.buildMenuItems.restore();
  });
});
