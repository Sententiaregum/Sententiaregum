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
import Suggestions from '../../../../components/portal/signup/Suggestions';
import TestUtils from 'react/lib/ReactTestUtils';
import ReactDOM from 'react-dom';
import chai from 'chai';

describe('Sugestions', () => {
  it('renders suggestions', () => {
    const suggestions = ['Ma27_2016', 'Ma27_2000'];
    const cmp         = TestUtils.renderIntoDocument(<Suggestions suggestions={suggestions} />);
    const node        = ReactDOM.findDOMNode(cmp.refs.list);

    chai.expect(node._childNodes.length).to.equal(2);
    for (const i in node._childNodes) {
      const suggestion = node._childNodes[i];
      const current    = suggestion._childNodes[0]._nodeValue;
      chai.expect(current).to.equal(suggestions[i]);
    }
  });

  it('renders nothing if no suggestions are provided', () => {
    const cmp = TestUtils.renderIntoDocument(<Suggestions suggestions={[]} />);
    chai.expect(ReactDOM.findDOMNode(cmp)).to.equal(null);
  });
});
